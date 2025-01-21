import { WishlistMainCheckboxUpdater } from './wishlistMainCheckboxUpdater'

const mainCheckboxlistener = document.querySelector('#toggle-checkboxes')

const turnOnListener = () => {
    if (!mainCheckboxlistener) {
        return;
    }
    new WishlistMainCheckboxUpdater().init();
};

turnOnListener();
