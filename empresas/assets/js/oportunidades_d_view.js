function showNotification(message, type = "success") {
    const notif = document.createElement("div");

    notif.innerText = message;

    notif.style.position = "fixed";
    notif.style.top = "20px";
    notif.style.right = "20px";
    notif.style.padding = "12px 20px";
    notif.style.borderRadius = "8px";
    notif.style.color = "white";
    notif.style.fontWeight = "bold";
    notif.style.zIndex = "9999";
    notif.style.boxShadow = "0 4px 10px rgba(0,0,0,0.2)";
    notif.style.opacity = "0";
    notif.style.transition = "all 0.4s ease";

    if(type === "error"){
        notif.style.backgroundColor = "#dc3545";
    } else {
        notif.style.backgroundColor = "#28a745";
    }

    document.body.appendChild(notif);

    setTimeout(() => {
        notif.style.opacity = "1";
        notif.style.transform = "translateY(10px)";
    }, 100);

    setTimeout(() => {
        notif.style.opacity = "0";
        setTimeout(() => notif.remove(), 400);
    }, 3000);
}


document.addEventListener("DOMContentLoaded", () => {

    const params = new URLSearchParams(window.location.search);

    if(params.get("status_updated") === "1"){
        showNotification("Estado actualizado correctamente", "success");
    }

});
