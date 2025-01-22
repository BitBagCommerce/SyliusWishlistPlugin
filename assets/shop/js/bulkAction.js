export class BulkAction {
    constructor(options) {
        this.cancelAction = options.cancelAction;
        this.performAction = options.performAction;
    }

    init() {
        this.performAction();
    }
}

export default BulkAction;
