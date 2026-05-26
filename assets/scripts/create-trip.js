let addressChoiceInput0 = document.getElementById('trip_choiceMethodAddress_0')
let addressChoiceInput1 = document.getElementById('trip_choiceMethodAddress_1')

let addressfield = document.getElementById('trip_address').parentNode.parentNode
let newAddressfield = document.getElementById('trip_newAddress').parentNode.parentNode
newAddressfield.style.display = 'none';

addressChoiceInput0.addEventListener('change', function (event) {displayForm(this);});
addressChoiceInput1.addEventListener('change', function (event) {displayForm(this);});

function displayForm(choicInput) {
    // Si il choisit de Créer l'addresse (1)
    if (parseInt(choicInput.value)) {
        addressfield.style.display = 'none';
        newAddressfield.style.display = 'block';
    } else {
        //Sinon, choisir l'addresse (0)
        addressfield.style.display = 'block';
        newAddressfield.style.display = 'none';
    }
}

