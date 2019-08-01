const neo4j = require('neo4j-driver').v1;
const {ConnectionDelegate} = require('@nesk/rialto');

class Row
{
	constructor(record) {
		this.obj = {}
		record.forEach(function(value, key) {
			console.log("Constructor: " + value.constructor.name)
			if (value.constructor.name === "Node"){
				this.obj[key] = Object.assign({
					id: value.identity.toInt(),
					labels: value.labels,
				}, value.properties);
			} else {
				this.obj[key] = value;
			}
		}.bind(this));
		console.error("Object ", this.obj)
	}

	toObject() {
		return this.obj;
	}
}

class Neo4j 
{
	constructor(parameters) {
		let host = parameters.host;
		let user = parameters.user;
		let password = parameters.password;
		this.driver = neo4j.driver("bolt + routing://" + host, neo4j.auth.basic(user, password));
	}

	close() {
		this.driver.close();
	}

	async run(query, parameters) {
		let res = [];
		let session = this.driver.session();
		await session.run(query, parameters)
			.then(function(result) {
				result.records.forEach((record) => res.push(new Row(record).toObject()));
				session.close();
			}.bind(this))
			.catch(function(error) {
				throw error;
			});
		return res;
	}
}

module.exports = class HelloWorldConnectionDelegate extends ConnectionDelegate
{
	constructor(options) {
		super(options)
		this.driverInstance = new Neo4j(options)
	}

    async handleInstruction(instruction, responseHandler, errorHandler)
    {
        // Define on which resource the instruction should be applied by default,
        // here we want to apply them on the "HelloWorld" class.
        await instruction.setDefaultResource(this.driverInstance);

        let value = null;

        try {
            // Try to execute the instruction
            value = await instruction.execute();
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