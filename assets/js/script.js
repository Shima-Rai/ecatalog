// -------------------------------------
// SIMPLE NOTIFICATION POPUP
// -------------------------------------
function showMessage(message, type = "success") {
    let box = document.createElement("div");
    box.className = "msg-box " + type;
    box.innerText = message;

    document.body.appendChild(box);

    setTimeout(() => {
        box.remove();
    }, 2500);
}

// -------------------------------------
// DELETE CONFIRMATION
// -------------------------------------
function confirmDelete() {
    return confirm("Are you sure you want to delete this item?");
}

// -------------------------------------
// AUTO FADE ALERTS
// -------------------------------------
setTimeout(() => {
    let alerts = document.querySelectorAll('.alert');
    alerts.forEach(a => a.style.display = "none");
}, 3000);
