export class CreateCopyToWishlistsListModal {
    constructor(
        config = {},
        actions = {
            performAction: () => {},
            cancelAction: () => {},
        }
    ) {
        this.config = config;
        this.defaultConfig = {
            headerTitle: 'Choose a wishlist to which selected items should be copied',
            cancelText: 'cancel',
            performText: 'perform',
            wishlistSelectionPlaceholder: 'My Wishlist…',
            datasetWishlistTargets: '[data-bb-wishlists]',
            datasetWishlistTargetsId: '[data-bb-wishlists-id]',
            datasetWishlistCurrentId: '[data-bb-current-wishlist-id]',
            confirmationModalClass: 'copy-confirmation-modal',
            confirmationModalheaderClass: 'copy-confirmation-modal__header',
            confirmationModalH2Class: 'copy-confirmation-modal__header--title',
            confirmationModalBodyClass: 'copy-confirmation-modal__body',
            confirmationModalBodyItemClass: 'copy-confirmation-modal__body--item',
            confirmationModalConfirmClass: 'copy-confirmation-modal__confirm',
            confirmationModalCancelBtnClass: 'copy-confirmation-modal__confirm--cancel',
            confirmationModalConfirmBtnClass: 'copy-confirmation-modal__confirm--perform',
        };
        this.actions = actions;
        this.finalConfig = {...this.defaultConfig, ...config};
        this.wishlistTargets = [...document.querySelectorAll(this.finalConfig.datasetWishlistTargets)]
        this.wishlistTargetsId = [...document.querySelectorAll(this.finalConfig.datasetWishlistTargetsId)]
        this.wishlistcurrent = document.querySelector(this.finalConfig.datasetWishlistCurrentId).dataset.bbCurrentWishlistId
        this.copyTarget
    }

    init() {
        if (this.config && typeof this.config !== 'object') {
            throw new Error('BitBag - CreateCopyToWishlistsListModal - given config is not valid - expected object');
        }
        this._renderModal();
    }

    _renderModal() {
        this.modal = this._modalTemplate();
        this.modal.classList.add('bitbag', 'copy-modal-initialization');
        this._modalActions(this.modal);
        document.querySelector('body').appendChild(this.modal);
        this.modal.classList.remove('copy-modal-initialization');
        this.modal.classList.add('copy-modal-initialized');
    }

    _modalTemplate() {
        const modal = document.createElement('div');
        modal.innerHTML = `    
        <form class=${this.finalConfig.confirmationModalClass}>
            <header class=${this.finalConfig.confirmationModalheaderClass}>
                <h2 class=${this.finalConfig.confirmationModalH2Class}>
                    ${this.finalConfig.headerTitle}
                </h2>
            </header>
            <section data-bb-target="wishlists" class=${this.finalConfig.confirmationModalBodyClass}>
                    
            </section>
            <section class=${this.finalConfig.confirmationModalConfirmClass}>
                <button type="button" data-bb-action="cancel" class=${this.finalConfig.confirmationModalCancelBtnClass}>
                    ${ this.finalConfig.cancelText }
                </button>
                <button type="button" data-bb-action="perform" class=${this.finalConfig.confirmationModalConfirmBtnClass}>
                    ${this.finalConfig.performText}
                </button>
            </section>
        </form>
        `;
        modal.querySelector('[data-bb-target="wishlists"]').appendChild(this._getWishlists());

        return modal;
    }

    _wishlistTemplate(wishlist) {
        return `    
            <option value="${wishlist.dataset.bbWishlistsId}" data-bb-wishlist-id="${wishlist.dataset.bbWishlistsId}">
                ${wishlist.dataset.bbWishlists}
            </option>
        `;
    }

    _getWishlists() {
        const select = document.createElement('select')
        select.name = 'wishlist'
        select.classList.add('form-select')
        select.insertAdjacentHTML("beforeend" ,`<option selected disabled>${this.finalConfig.wishlistSelectionPlaceholder}</option>`)

        this.wishlistTargets.forEach(wishlist => {
            select.insertAdjacentHTML("beforeend" , this._wishlistTemplate(wishlist))
        });
        return select;
    }

    _matchWishlists(targetWishlists) {
        this.wishlistTargetsId.forEach(wishlist => {
            if (wishlist.dataset.bbWishlists == targetWishlists.value) {
                this.copyTarget = wishlist.dataset.bbWishlistsId
            }
        });
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
            this._matchWishlists(document.querySelector('[data-bb-target] select'));
            this.actions.performAction(this.wishlistcurrent, this.copyTarget);
            this._closeModal();
        });
    }

    _closeModal() {
        this.modal.remove();
    }
}

export default CreateCopyToWishlistsListModal;
