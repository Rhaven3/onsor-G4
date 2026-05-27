import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        url: String
    }

    open(event) {
        event.preventDefault();

        let saisie = prompt("Veuillez le message d'annulation :");

        if (saisie === null) {
            return;
        }
        const urlFinale = `${this.urlValue}?saisie=${encodeURIComponent(saisie)}`;
        window.location.href = urlFinale;
    }
}
