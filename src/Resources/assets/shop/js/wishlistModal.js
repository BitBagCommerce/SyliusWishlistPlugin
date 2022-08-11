export class WishlistModal {
    constructor(
        config = {},
        actions = {
            performAction: () => {},
            cancelAction: () => {},
        }
    ) {
        this.config = config;
        this.defaultConfig = {
            headerTitle: 'Modal title',
            cancelText: 'cancel',
            performText: 'perform',
            wishlistBodyContent: 'Modal body',
            wishlistFormName: 'wishlist',
            datasetWishlistTargets: '[data-bb-wishlists]',
            wishlistModalClass: 'wishlist-confirmation-modal',
            wishlistheaderClass: 'wishlist-confirmation-modal__header',
            wishlistH2Class: 'wishlist-confirmation-modal__header--title',
            wishlistBodyClass: 'wishlist-confirmation-modal__body',
            wishlistBodyItemClass: 'wishlist-confirmation-modal__body--input',
            wishlistConfirmClass: 'wishlist-confirmation-modal__confirm',
            wishlistCancelBtnClass: 'wishlist-confirmation-modal__confirm--cancel',
            wishlistConfirmBtnClass: 'wishlist-confirmation-modal__confirm--perform',
        };
        this.actions = actions;
        this.finalConfig = {...this.defaultConfig, ...config};
    }

    init() {
        if (this.config && typeof this.config !== 'object') {
            throw new Error('BitBag - WishlistsListModal - given config is not valid - expected object');
        }

        this._renderModal();
    }

    _renderModal() {
        this.modal = this._modalTemplate();
        this.modal.classList.add('bitbag', 'wishlist-modal-initialization');
        this._modalActions(this.modal);
        document.querySelector('body').appendChild(this.modal);
        this.modal.classList.remove('wishlist-modal-initialization');
        this.modal.classList.add('wishlist-modal-initialized');
    }

    _modalTemplate() {
        const modal = document.createElement('div');

        modal.innerHTML = `    
            <form name="${this.finalConfig.wishlistFormName}_save" id="${this.finalConfig.wishlistFormName}" method="post" class=${this.finalConfig.wishlistModalClass}>
                <header class=${this.finalConfig.wishlistheaderClass}>
                    <h2 class=${this.finalConfig.wishlistH2Class}>
                        ${this.finalConfig.headerTitle}
                    </h2>
                </header>
                <section data-bb-target="wishlists" class=${this.finalConfig.wishlistBodyClass}>
                    ${this.finalConfig.wishlistBodyContent}
                </section>
                <section class=${this.finalConfig.wishlistConfirmClass}>
                    <button type="button" data-bb-action="cancel" class=${this.finalConfig.wishlistCancelBtnClass}>
                        ${this.finalConfig.cancelText}
                    </button>
                    <button type="button" data-bb-action="perform" id="${this.finalConfig.wishlistFormName}_save" class=${this.finalConfig.wishlistConfirmBtnClass}>
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
        const input = template.querySelector('[data-bb-target="wishlists"] > [data-bb-target="input"]');

        cancelBtn.addEventListener('click', () => {
            this.actions.cancelAction();
            this._closeModal();
        });

        confirmBtn.addEventListener('click', () => {
            if(input && input.value === '') {
                this._triggerInputError();

                return;
            }

            this.actions.performAction();
            this._closeModal();
        });
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

export default WishlistModal;
