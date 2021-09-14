import { WishlistVariantButton } from './WishlistVariantButton';

const WishlistVariantElements = [...document.querySelectorAll('[data-bb-toggle="wishlist-variant"]')];
const WishlistVariantButtonList = WishlistVariantElements.map(button => new WishlistVariantButton(button))

export default WishlistVariantButtonList;