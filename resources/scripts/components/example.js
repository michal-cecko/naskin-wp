import Commons from './commons.js'

class Example extends Commons {
    constructor() {
        super();

        console.log("Example JS has been loaded.")

        this._prepare();
    }

    _prepare() {
        //Add logic here...
    }
}

new Example();

export {}
