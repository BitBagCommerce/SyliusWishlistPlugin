export class WishlistVariantPrice {
    constructor(node) {
        if (!node?.nodeType) throw new Error("The first parameter must be a NodeElement");

        this.item = document.querySelector(node.dataset.bbTarget);
        this.nodePrice = node;
        this.pricing = JSON.parse(node.dataset.bbPricing || '');
        this.selectors = this.item.querySelectorAll('select[data-name]');
        this.selectedVariant = {}
    }

    init() {
        this.selectors.forEach(select => {
            this._updateSelectedVariant(select);
            this.selectedVariant[select.dataset.name] = select.value;
            select.addEventListener('change', this._onChangeSelector.bind(this));
        })

        this.nodePrice.removeAttribute('data-bb-pricing');

        this._updatePrice()
    }

    _onChangeSelector(event) {
        this._updateSelectedVariant(event.currentTarget);
    }

    _updateSelectedVariant(select) {
        this.selectedVariant[select.dataset.name] = select.value;
        this._updatePrice();
    }

    _updatePrice() {
        const matches = (obj, source) =>
            Object.keys(source).every(key => obj.hasOwnProperty(key) && obj[key] === source[key]);

        const price = this.pricing.find(price => matches(price, this.selectedVariant));

        this.nodePrice.innerHTML = price.value;
    }
}

export default WishlistVariantPrice;
