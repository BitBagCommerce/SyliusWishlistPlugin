export class AddWishlistModal {
    constructor(
        config = {},
        actions = {
            performAction: () => {},
            cancelAction: () => {},
        }
    ) {
        this.config = config;
        this.defaultConfig = {
            headerTitle: 'Choose name for your new wishlist',
            cancelText: 'cancel',
            performText: 'perform',
            datasetWishlistTargets: '[data-bb-wishlists]',
            addWishlistModalClass: 'add-wishlist-confirmation-modal',
            addWishlistheaderClass: 'add-wishlist-confirmation-modal__header',
            addWishlistH2Class: 'add-wishlist-confirmation-modal__header--title',
            addWishlistBodyClass: 'add-wishlist-confirmation-modal__body',
            addWishlistBodyItemClass: 'add-wishlist-confirmation-modal__body--input',
            addWishlistConfirmClass: 'add-wishlist-confirmation-modal__confirm',
            addWishlistCancelBtnClass: 'add-wishlist-confirmation-modal__confirm--cancel',
            addWishlistConfirmBtnClass: 'add-wishlist-confirmation-modal__confirm--perform',
        };
        this.actions = actions;
        this.finalConfig = {...this.defaultConfig, ...config};
    }

    init() {
        if (this.config && typeof this.config !== 'object') {
            throw new Error('BitBag - CreateCopyToWishlistsListModal - given config is not valid - expected object');
        }
        this._renderModal();
    }

    _renderModal() {
        this.modal = this._modalTemplate();
        this.modal.classList.add('bitbag', 'add-wishlist-modal-initialization');
        this._modalActions(this.modal);
        document.querySelector('body').appendChild(this.modal);
        this.modal.classList.remove('add-wishlist-modal-initialization');
        this.modal.classList.add('add-wishlist-modal-initialized');
    }

    _modalTemplate() {
        const modal = document.createElement('div');
        modal.innerHTML = `    
        <form name="create_new_wishlist_save" id="create_new_wishlist" method="post" class=${this.finalConfig.addWishlistModalClass}>
            <header class=${this.finalConfig.addWishlistheaderClass}>
                <h2 class=${this.finalConfig.addWishlistH2Class}>
                    ${this.finalConfig.headerTitle}
                </h2>
            </header>
            <section data-bb-target="wishlists" class=${this.finalConfig.addWishlistBodyClass}>
                <input type="text" id="create_new_wishlist_name" name="create_new_wishlist[name]" required="required" class=${this.finalConfig.addWishlistBodyItemClass} data-bb-target="input">
                <div class="ui red pointing label validation-error hidden" data-bb-target="error">Please enter wishlist name.</div>
            </section>
            <section class=${this.finalConfig.addWishlistConfirmClass}>
                <button type="button" data-bb-action="cancel" class=${this.finalConfig.addWishlistCancelBtnClass}>
                    ${this.finalConfig.cancelText}
                </button>
                <button type="submit" data-bb-action="perform" id="create_new_wishlist_save" class=${this.finalConfig.addWishlistConfirmBtnClass}>
                    ${this.finalConfig.performText}
                </button>
            </section>
        </form>
        `;

        return modal;
    }

    _modalActions(template) {
        const cancelBtn = template.querySelector('[data-bb-action="cancel"]');
        const confirmBtn = template.querySelector('[data-bb-action="perform"]');

        cancelBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.actions.cancelAction();
            this._closeModal();
        });

        confirmBtn.addEventListener('click', (e) => {
            e.preventDefault();

            if(this._isInputEmpty()) {
                this._triggerInputError();

                return;
            }

            this.actions.performAction();
            this._closeModal();
        });
    }

    _isInputEmpty() {
        const input = document.querySelector('[data-bb-target="wishlists"] > [data-bb-target="input"]');

        return input.value === '';
    }

    _triggerInputError() {
        const body = document.querySelector('[data-bb-target="wishlists"]');
        const input = body.querySelector('[data-bb-target="input"]');
        const div = body.querySelector('[data-bb-target="error"]');

        input.classList.add('error');
        div.classList.remove('hidden');
    }

    _closeModal() {
        this.modal.remove();
    }
}

export default AddWishlistModal;
