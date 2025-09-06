// ================= DATA =================
// You can replace this with AJAX calls later to fetch from database
let appointments = [
    {id:1,date:"2025-09-01",time:"10:30 AM",pet:"Charlie",owner:"Sarah Johnson",reason:"Annual Checkup",status:"scheduled",prescription:"",record:""},
    {id:2,date:"2025-09-01",time:"11:00 AM",pet:"Milo",owner:"John Doe",reason:"Vaccination",status:"completed",prescription:"Vaccine given",record:"All good"},
    {id:3,date:"2025-09-02",time:"12:00 PM",pet:"Lucy",owner:"Emma Watson",reason:"Dental Check",status:"cancelled",prescription:"",record:""},
    {id:4,date:"2025-09-02",time:"12:30 PM",pet:"Bella",owner:"James Brown",reason:"Follow-up",status:"ongoing",prescription:"",record:""}
];

// Split into upcoming and completed arrays
let upcomingAppointments = appointments.filter(a => ["scheduled","ongoing"].includes(a.status));
let completedAppointments = appointments.filter(a => ["completed","cancelled"].includes(a.status));

// ================= RENDER TABLE =================
function renderTable(tableId, data) {
    const tbody = document.getElementById(tableId);
    tbody.innerHTML = "";
    data.forEach(appt => {
        const tr = document.createElement("tr");
        tr.dataset.id = appt.id;
        tr.dataset.status = appt.status;
        tr.dataset.date = appt.date;

        tr.innerHTML = `
            <td>${appt.id}</td>
            <td>${appt.date}</td>
            <td>${appt.time}</td>
            <td>${appt.pet}</td>
            <td>${appt.owner}</td>
            <td>${appt.reason}</td>
            <td>${appt.status.charAt(0).toUpperCase() + appt.status.slice(1)}</td>
            ${tableId === "completedTable" ? `
            <td>${appt.status==="completed" ? `<button class="btn navy recordBtn" data-id="${appt.id}" data-has-record="${appt.record ? 1 : 0}">${appt.record ? "Edit" : "Add"}</button>` : ""}</td>
            <td>${appt.status==="completed" ? `<button class="btn navy prescriptionBtn" data-id="${appt.id}" data-has-prescription="${appt.prescription ? 1 : 0}">${appt.prescription ? "Edit" : "Add"}</button>` : ""}</td>
            ` : ""}
        `;
        tbody.appendChild(tr);
    });

    // Attach click events for prescription and record buttons
    if(tableId === "completedTable"){
        document.querySelectorAll(".recordBtn").forEach(btn => {
            btn.addEventListener("click", () => {
                let id = btn.dataset.id;
                let hasRecord = btn.dataset.hasRecord === "1";
                let action = hasRecord ? "edit" : "add";
                window.location.href = `medical-records.php?appointment_id=${id}&action=${action}`;
            });
        });

        document.querySelectorAll(".prescriptionBtn").forEach(btn => {
            btn.addEventListener("click", () => {
                let id = btn.dataset.id;
                let hasPrescription = btn.dataset.hasPrescription === "1";
                let action = hasPrescription ? "edit" : "add";
                window.location.href = `prescriptions.php?appointment_id=${id}&action=${action}`;
            });
        });
    }
}

// ================= INITIAL RENDER =================
renderTable("upcomingTable", upcomingAppointments);
renderTable("completedTable", completedAppointments);

// ================= FILTER & SEARCH =================
function filterTable(tableId, data, dateInputId, statusInputId, searchInputId){
    const dateVal = document.getElementById(dateInputId).value;
    const statusVal = document.getElementById(statusInputId).value.toLowerCase();
    const searchVal = document.getElementById(searchInputId).value.toLowerCase();

    let filtered = data.filter(a => {
        let match = true;
        if(dateVal) match = match && a.date === dateVal;
        if(statusVal) match = match && a.status === statusVal;
        if(searchVal){
            match = match && (a.pet.toLowerCase().includes(searchVal) ||
                             a.owner.toLowerCase().includes(searchVal) ||
                             a.reason.toLowerCase().includes(searchVal));
        }
        return match;
    });

    renderTable(tableId, filtered);
}

// Upcoming filters
document.getElementById("applyUpcomingFilter").addEventListener("click", () => {
    filterTable("upcomingTable", upcomingAppointments, "upcomingDateFilter", "upcomingStatusFilter", "searchUpcoming");
});
document.getElementById("searchUpcoming").addEventListener("keyup", () => {
    filterTable("upcomingTable", upcomingAppointments, "upcomingDateFilter", "upcomingStatusFilter", "searchUpcoming");
});

// Completed filters
document.getElementById("applyCompletedFilter").addEventListener("click", () => {
    filterTable("completedTable", completedAppointments, "completedDateFilter", "completedStatusFilter", "searchCompleted");
});
document.getElementById("searchCompleted").addEventListener("keyup", () => {
    filterTable("completedTable", completedAppointments, "completedDateFilter", "completedStatusFilter", "searchCompleted");
});
