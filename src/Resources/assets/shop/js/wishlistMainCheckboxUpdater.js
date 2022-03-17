export class WishlistMainCheckboxUpdater {
    constructor(
        config = {},
    ) {
        this.config = config;
        this.defaultConfig = {
            mainCheckboxId: '#toggle-checkboxes',
            checkboxesData: '[data-bb-checkboxes] input',
            bulkActionsBtnClass: '.bb-colective-actions',
        };
        this.finalConfig = {...this.defaultConfig, ...config};
        this.mainCheckbox = document.querySelector(this.finalConfig.mainCheckboxId)
        this.checkboxes = Array.prototype.slice.call( document.querySelectorAll(this.finalConfig.checkboxesData) )
    }

    init() {    
        if (this.config && typeof this.config !== 'object') {
            throw new Error('BitBag - WishlistMainCheckboxUpdater - given config is not valid - expected object');
        }
        this._connectListeners();
    }

    _toggleCheckboxesON() {
        for (let index = 0; index < this.checkboxes.length; index++) {
            this.checkboxes[index].checked = true;    
        }  
    }

    _toggleCheckboxesOFF() {
        for (let index = 0; index < this.checkboxes.length; index++) {
            this.checkboxes[index].checked = false;    
        }  
    }

    _checkCheckboxes() {
        let allUnChecked = true;
        this.checkboxes.forEach(checkbox => {
            if (checkbox.checked == true) {
                allUnChecked = false
            }
        });
        return allUnChecked;
    }

    _handleCheckboxes(){
        const allUnChecked = this._checkCheckboxes();

        if (allUnChecked) {
            this.mainCheckbox.checked = false;
            this._disableBulkActionsBtn()
        } else {
            this.mainCheckbox.checked = true;
            this._enableBulkActionsBtn()
        }
    }

    _handleMainCheckboxToggle(){
        if (this.mainCheckbox.checked != true) {
            this._toggleCheckboxesOFF()
            this._disableBulkActionsBtn()
            return;
        }
        this._toggleCheckboxesON();
        this._enableBulkActionsBtn()
    }

    _enableBulkActionsBtn() {
        const BulkBtn = document.querySelector(this.finalConfig.bulkActionsBtnClass)
        BulkBtn.classList.remove('disabled')
        BulkBtn.classList.add('enabled')
    }

    _disableBulkActionsBtn() {
        const BulkBtn = document.querySelector(this.finalConfig.bulkActionsBtnClass)
        BulkBtn.classList.remove('enabled')
        BulkBtn.classList.add('disabled')
    }


    _connectListeners = () => {
        this.checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                this._handleCheckboxes();
            })
        });

        this.mainCheckbox.addEventListener('change', (e) => {
            this._handleMainCheckboxToggle();
        });        
    };
}

export default WishlistMainCheckboxUpdater;
