document.querySelectorAll('input[name="site_choice"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('site-existing').style.display = this.value === 'existing' ? 'block' : 'none';
        document.getElementById('site-new').style.display = this.value === 'new' ? 'block' : 'none';
    });
});
