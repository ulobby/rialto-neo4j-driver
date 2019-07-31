const fs = require('fs'),
    {ConnectionDelegate} = require('@nesk/rialto');

module.exports = class FileSystemConnectionDelegate extends ConnectionDelegate
{
    handleInstruction(instruction, responseHandler, errorHandler)
    {
        // Define on which resource the instruction should be applied by default,
        // here we want to apply them on the "fs" module.
        instruction.setDefaultResource(fs);

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