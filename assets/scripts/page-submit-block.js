let $form = document.getElementById("form");

let $saveBtn = document.getElementById('button-save');
let $publishBtn = document.getElementById('button-publish');

$form.addEventListener('submit', (e) => {
    console.log("C'est cliqué !")
    $saveBtn.disabled = true;
    $publishBtn.disabled = true;
})
