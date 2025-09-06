// vaccinations.js

document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchBar");
    const table = document.getElementById("vaccinationTable");
    const tbody = table.querySelector("tbody");

    // ================= Live Search / Filter =================
    searchInput.addEventListener("keyup", function () {
        const filter = searchInput.value.toLowerCase();

        Array.from(tbody.rows).forEach(row => {
            const pet = row.cells[0].textContent.toLowerCase();
            const vaccine = row.cells[1].textContent.toLowerCase();
            const dateGiven = row.cells[2].textContent.toLowerCase();
            const nextDue = row.cells[3].textContent.toLowerCase();

            if (
                pet.includes(filter) ||
                vaccine.includes(filter) ||
                dateGiven.includes(filter) ||
                nextDue.includes(filter)
            ) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });
});
