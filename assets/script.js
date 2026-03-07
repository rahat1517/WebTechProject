function confirmDelete() {
    return confirm("Are you sure you want to delete this item?");
}
function confirmLogout() {
    return confirm("Are you sure you want to logout?");
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
