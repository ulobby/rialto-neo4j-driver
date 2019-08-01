const {ConnectionDelegate} = require('@nesk/rialto');

class HelloWorld
{
	constructor(lame) {
		this.lame = lame
	}

	printHello() {
		return "Hello " + this.lame
	}
}

module.exports = class HelloWorldConnectionDelegate extends ConnectionDelegate
{
	constructor(options) {
		super(options)
		this.helloInstance = new HelloWorld(options.name)
	}

    async handleInstruction(instruction, responseHandler, errorHandler)
    {
        // Define on which resource the instruction should be applied by default,
        // here we want to apply them on the "HelloWorld" class.
        await instruction.setDefaultResource(this.helloInstance);

        let value = null;

        try {
            // Try to execute the instruction
            value = instruction.execute();
        } catch (error) {
            // If the instruction fails and the user asked to catch errors (see the `tryCatch` property in the API),
            // send it with the error handler.
            if (instruction.shouldCatchErrors()) {
                return errorHandler(error);
            }

            throw error;
        }

        // Send back the value returned by the instruction
        responseHandler(value);
    }
}