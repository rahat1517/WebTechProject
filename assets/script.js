let deleteFormId = null;

function openDeleteModal(id) {
    deleteFormId = "deleteForm" + id;
    const modal = document.getElementById("deleteModal");
    if (modal) {
        modal.style.display = "block";
    }
}

function closeDeleteModal() {
    const modal = document.getElementById("deleteModal");
    if (modal) {
        modal.style.display = "none";
    }
}

const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
if (confirmDeleteBtn) {
    confirmDeleteBtn.onclick = function () {
        if (deleteFormId) {
            document.getElementById(deleteFormId).submit();
        }
    };
}

function openLogoutModal() {
    const modal = document.getElementById("logoutModal");
    if (modal) {
        modal.style.display = "block";
    }
}

function closeLogoutModal() {
    const modal = document.getElementById("logoutModal");
    if (modal) {
        modal.style.display = "none";
    }
}
const searchInput = document.getElementById("tableSearch");
if (searchInput) {
    searchInput.addEventListener("keyup", function () {
        const filter = searchInput.value.toLowerCase();
        const rows = document.querySelectorAll("#dataTable tr");

        rows.forEach((row, index) => {
            if (index === 0) return;
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });
}
