function showCalendarView(view) {
document.getElementById('btn-today').classList.remove('active');
document.getElementById('btn-week').classList.remove('active');
document.getElementById('btn-month').classList.remove('active');
document.getElementById('btn-' + view).classList.add('active');
document.getElementById('calendar-today').classList.remove('active');
document.getElementById('calendar-week').classList.remove('active');
document.getElementById('calendar-month').classList.remove('active');
document.getElementById('calendar-' + view).classList.add('active');
}

function openDetailsFromEl(el) {
document.getElementById('dPet').textContent = el.dataset.pet || '';
document.getElementById('dClient').textContent = el.dataset.client || '';
document.getElementById('dVet').textContent = el.dataset.vet || '';

if (el.dataset.date) document.getElementById('dDate').value = el.dataset.date;
if (el.dataset.time) document.getElementById('dTime').value = el.dataset.time;

document.getElementById('detailsModal').classList.remove('hidden');
}

function openAddModal() {
document.getElementById('addModal').classList.remove('hidden');
}

function closeModal(id) {
document.getElementById(id).classList.add('hidden');
}
