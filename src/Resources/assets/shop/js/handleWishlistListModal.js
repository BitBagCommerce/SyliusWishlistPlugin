const copyToWishlistTargetBtn = document.querySelector('[data-bb-wl-list-modal-target]');
const copyToWishlistPopup = document.querySelector('[data-bb-copy-to-wl-popup]')
const copyToWishlistCancelBtn = document.querySelector('[data-bb-action="cancel"]');

const setDisabled = (element) => {
    element.classList.remove('copy-modal-initialized')
    element.classList.add('copy-modal-initialization')
};

const setEnabled = (element) => {
    element.classList.remove('copy-modal-initialization')
    element.classList.add('copy-modal-initialized')
};

const connectListeners = () => {
    copyToWishlistTargetBtn.addEventListener('click', (e) => {
        setEnabled(copyToWishlistPopup)
    });
    
    copyToWishlistCancelBtn.addEventListener('click', (e) => {
        setDisabled(copyToWishlistPopup);
    });
};

const turnOnListener = () => {
    if (!copyToWishlistTargetBtn || !copyToWishlistPopup || !copyToWishlistCancelBtn) {
        return;
    }
    
    connectListeners();
};

turnOnListener();
