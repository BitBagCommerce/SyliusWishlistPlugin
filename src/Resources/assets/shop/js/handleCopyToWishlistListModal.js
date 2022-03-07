import { CreateCopyToWishlistsListModal } from './copyToWishlistsListModal'

const copyToWishlistBtn = document.querySelector('[data-bb-wl-list-modal-target="choose-wishlist-button"]')
const setAddWishlistModal = () => {
    copyToWishlistBtn.addEventListener('click', (e) => {
        e.preventDefault();

        new CreateCopyToWishlistsListModal(
            {},
            {
                cancelAction: () => {},
                performAction: async () => {
                    const form = document.querySelector('#wishlist_form');
                    const formData = new FormData(form);
                    const wishlistsBtn = document.querySelector('[data-bb-wishlist-enter]');
                    const chosenWishlist = document.querySelector('.copy-confirmation-modal select').value;
                    const url = `${window.location.href}/copy/${chosenWishlist}`;
                    const csrfToken = document.querySelector("[data-bb-csrf]").dataset.bbCsrf;
                    const headers = new Headers({
                        'X-CSRF-TOKEN': csrfToken
                    });

                    const requestConfig = {
                        method: 'POST',
                        headers: headers,
                        body: formData
                    };

                    try {
                        const response = await fetch(url, requestConfig);
                        const data = await response.json();
                        wishlistsBtn.classList.add('bb-copy-to-wishlist-sukces')
                        setTimeout(() => {
                            wishlistsBtn.classList.remove('bb-copy-to-wishlist-sukces')
                        }, 900);
                    } catch (error) {
                        wishlistsBtn.classList.add('bb-copy-to-wishlist-faliure')
                        setTimeout(() => {
                            wishlistsBtn.classList.remove('bb-copy-to-wishlist-faliure')
                        }, 900);
                        console.error(error);
                    } finally {
                        location.reload();
                    }
                },
            }
        ).init();
    });

};

const turnOnListener = () => {
    if (!copyToWishlistBtn) {
        return;
    }
    setAddWishlistModal();
};

turnOnListener();
