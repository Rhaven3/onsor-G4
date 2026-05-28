let flashMessage = document.getElementById('flash-message-container');

flashMessage.addEventListener('animationend', (e) => {
    flashMessage.remove();
})
