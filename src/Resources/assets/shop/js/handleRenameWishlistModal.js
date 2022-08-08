import { RenameWishlistModal } from './renameWishlistModal';

const renameWishlistBtns = document.querySelectorAll('[data-wishlist-rename-id]');

const setAddWishlistModal = () => {
    renameWishlistBtns.forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            new RenameWishlistModal(
                {},
                {
                    cancelAction: () => {},
                    performAction: async () => {
                        const form = document.querySelector('#edit_wishlist_name')
                        const formValue = form.querySelector('#edit_wishlist_name_name');
    
                        if (!formValue?.value) {
                            alert('Wishlist name cannot be empty');
    
                            return;
                        }
    
                        const url = `/wishlists/${btn.dataset.wishlistRenameId}/edit`
                        const formData = new FormData(form);
                        const csrfToken = document.querySelector("[data-bb-csrf]").dataset.bbCsrf
    
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
                            location.reload()
                        }
                    },
                }
            ).init();
        });
    });
};

const turnOnListener = () => {
    if (!renameWishlistBtns) {
        return;
    }
    setAddWishlistModal();
};

turnOnListener();
