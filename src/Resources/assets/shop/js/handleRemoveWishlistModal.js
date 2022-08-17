import { WishlistModal } from './wishlistModal';

const removeWishlistBtns = document.querySelectorAll('[data-wishlist-remove-id]');
const wishlistFormName = 'remove_wishlist';

const setWishlistModal = () => {
    removeWishlistBtns.forEach(btn => {
        btn.addEventListener('click', () => {   
            new WishlistModal(
                {
                    headerTitle: 'Remove wishlist',
                    wishlistFormName: wishlistFormName,
                    wishlistBodyContent: 'Are you sure?'
                },
                {
                    cancelAction: () => {},
                    performAction: async () => {
                        const form = document.querySelector(`#${wishlistFormName}`);
                        
                        const wishlistId = btn.dataset.wishlistRemoveId;
                        const url = `/wishlists/${wishlistId}/remove`;
                        const formData = new FormData(form);
                        const csrfToken = document.querySelector("[data-bb-csrf]").dataset.bbCsrf;
    
                        const headers = new Headers({
                            'X-CSRF-TOKEN': csrfToken
                        });
    
                        const requestConfig = {
                            method: 'POST',
                            headers: headers, 
                            body: formData
                        }
                        
                        try {
                            const response = await fetch(url, requestConfig);
                            const data = await response.json();
                            
                        } catch (error) {
                            console.error(error);
                        } finally {
                            location.reload();
                        }    
                    },
                }
            ).init();
        });
    })
};

const turnOnListener = () => {
    if (!removeWishlistBtns) {
        return;
    }

    setWishlistModal();
};

turnOnListener();
