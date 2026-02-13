<?php
// Migration: add vet suspension + on-leave flags to `vets`
// Safe to re-run (checks information_schema before altering)

require_once __DIR__ . '/../../config/connect.php';

$pdo = db();

function columnExists(PDO $pdo, string $table, string $column): bool {
    $stmt = $pdo->prepare(
        "SELECT COUNT(*)\n" .
        "FROM information_schema.columns\n" .
        "WHERE table_schema = DATABASE()\n" .
        "  AND table_name = ?\n" .
        "  AND column_name = ?"
    );
    $stmt->execute([$table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

echo "<h2>Migration: vets suspension + leave flags</h2>";

try {
    if (!columnExists($pdo, 'vets', 'is_suspended')) {
        $pdo->exec("ALTER TABLE vets ADD COLUMN is_suspended TINYINT(1) NOT NULL DEFAULT 0 AFTER available");
        echo "<p style='color:green;'>✓ Added vets.is_suspended</p>";
    } else {
        echo "<p>• vets.is_suspended already exists</p>";
    }

    if (!columnExists($pdo, 'vets', 'suspended_at')) {
        $pdo->exec("ALTER TABLE vets ADD COLUMN suspended_at TIMESTAMP NULL DEFAULT NULL AFTER is_suspended");
        echo "<p style='color:green;'>✓ Added vets.suspended_at</p>";
    } else {
        echo "<p>• vets.suspended_at already exists</p>";
    }

    if (!columnExists($pdo, 'vets', 'is_on_leave')) {
        $pdo->exec("ALTER TABLE vets ADD COLUMN is_on_leave TINYINT(1) NOT NULL DEFAULT 0 AFTER suspended_at");
        echo "<p style='color:green;'>✓ Added vets.is_on_leave</p>";
    } else {
        echo "<p>• vets.is_on_leave already exists</p>";
    }

    // Backfill: preserve existing behavior where available=0 means not available (treat as On Leave by default).
    // Suspension will now be controlled separately via is_suspended.
    $pdo->exec("UPDATE vets SET is_on_leave = CASE WHEN available = 0 THEN 1 ELSE 0 END WHERE is_suspended = 0");
    echo "<p style='color:green;'>✓ Backfilled vets.is_on_leave from available</p>";

    // Note: On TiDB/MySQL, DDL statements (ALTER TABLE) may implicitly commit.
    // Avoid committing/rolling back unless a transaction is actually active.
    if ($pdo->inTransaction()) {
        $pdo->commit();
    }
    echo "<hr><h3 style='color:green;'>✅ Migration complete</h3>";
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "<p style='color:red;'><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}
