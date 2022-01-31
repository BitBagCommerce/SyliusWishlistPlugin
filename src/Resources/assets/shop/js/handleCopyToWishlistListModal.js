import { CreateCopyToWishlistsListModal } from './copyToWishlistsListModal'

const copyToWishlistBtn = document.querySelector('[data-bb-wl-list-modal-target="choose-wishlist-button"]')

const setAddWishlistModal = () => {
    copyToWishlistBtn.addEventListener('click', (e) => {
        e.preventDefault();
        new CreateCopyToWishlistsListModal(
            {},
            {
                cancelAction: () => {},
                performAction: async (current, target) => {
                    const form = e.target.closest('form');
                    const formData = new FormData(form);
                    const wishlistsBtn = document.querySelector('[href="/wishlists"]');
                    const url = '/wishlist/' + current + '/copy/' + target;
                    
                    const csrfToken = document.querySelector("[data-bb-csrf]").dataset.bbCsrf
                    
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

