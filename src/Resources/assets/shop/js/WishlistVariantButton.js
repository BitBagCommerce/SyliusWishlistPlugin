const DEFAULT_SELECTORS = {
    form: '#sylius-product-adding-to-cart'
}

export class WishlistVariantButton {
    constructor(node, options = {}) {
        if (!node?.nodeType) throw new Error("The first parameter must be a NodeElement")

        const { selectors } = options;

        this.node = node;
        this.selectors = Object.assign({}, DEFAULT_SELECTORS, selectors)
    }

    init() {
        this.node.addEventListener('click', event => this._addVariantToWishlist(event));
    }

    async _addVariantToWishlist(event) {
        event.preventDefault();

        const url = await this._getWishlistVariantUri();

        this._redirectToWishlist(url);
    }

    async _getWishlistVariantUri() {
        try {
            const form = document.querySelector(this.selectors.form);
            const data = new FormData(form);

            data.append(this.node.name, '');

            const response = await fetch(form.action, { method: 'POST', body: data });

            return await response.text();
        } catch (error) {
            console.error(error);
        }
    }

    _redirectToWishlist(path) {
        location.href = path;
    }
}

export default WishlistVariantButton;
