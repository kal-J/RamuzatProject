

class TransactionDate extends HTMLElement {
    constructor() {
        super();

    }
    connectedCallback() {
        this.innerHTML = `
        <select id=${this.getAttribute('select_id')} class="form-control transaction-date" name=${this.getAttribute('name')} data-bind='options: ${this.getAttribute('allowed_transaction_dates')}, optionsCaption: "---select---" ' style="width: 100%">
        </select>
        `; 

        if(this.getAttribute('dropdownParentId')) {
            $(this.querySelector('.transaction-date')).select2({
                dropdownParent: $(`#${this.getAttribute('dropdownParentId')}`)
            });
        } else {
            $(this.querySelector('.transaction-date')).select2();
        }
        
    }
}

window.customElements.define('transaction-date', TransactionDate);