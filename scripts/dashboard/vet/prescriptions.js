document.addEventListener("DOMContentLoaded", () => {
    const table = document.getElementById("recordsTable");
    const tbody = table.querySelector("tbody");
    const form = document.querySelector(".appointment-list form");

    // ---------------- Edit Button ----------------
    tbody.querySelectorAll("form[action='prescriptions.php']").forEach(editForm => {
        const actionInput = editForm.querySelector("input[name='action']");
        if (actionInput && actionInput.value === "edit") {
            editForm.addEventListener("submit", function(e) {
                e.preventDefault(); // prevent page reload
                const row = this.closest("tr");
                if (!row) return;

                // Fill the main form with row data
                form.querySelector("input[name='id']").value = this.querySelector("input[name='id']").value;
                form.querySelector("input[name='appointment_id']").value = row.cells[3].textContent;
                form.querySelector("input[name='pet']").value = row.cells[1].textContent;
                form.querySelector("input[name='date']").value = row.cells[2].textContent;
                form.querySelector("input[name='medicine']").value = row.cells[4].textContent;
                form.querySelector("input[name='dosage']").value = row.cells[5].textContent;
                form.querySelector("input[name='frequency']").value = row.cells[6].textContent;
                form.querySelector("input[name='duration']").value = row.cells[7].textContent;
                form.querySelector("textarea[name='instructions']").value = row.cells[8].textContent;

                // Scroll to form
                form.scrollIntoView({ behavior: "smooth" });
            });
        }
    });

    // ---------------- Delete Confirmation ----------------
    tbody.querySelectorAll("form[action='prescriptions.php']").forEach(delForm => {
        const actionInput = delForm.querySelector("input[name='action']");
        if (actionInput && actionInput.value === "delete") {
            delForm.addEventListener("submit", e => {
                if (!confirm("Are you sure you want to DELETE this prescription?")) {
                    e.preventDefault();
                }
            });
        }
    });

    // ---------------- Medical Record Button ----------------
    tbody.querySelectorAll("form[action='medical-records.php']").forEach(medForm => {
        medForm.addEventListener("submit", e => {
            // appointment_id is already in hidden input; form submission works
        });
    });
});
