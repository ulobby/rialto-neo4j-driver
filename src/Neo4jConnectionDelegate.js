const neo4j = require('neo4j-driver').v1;
const {ConnectionDelegate} = require('@nesk/rialto');

class Row
{
	constructor(record) {
		this.obj = {}
		record.forEach(function(value, key) {
			// Serialize the returned record to an object
			if (value.constructor.name === "Node"){
				this.obj[key] = Object.assign({
					id: value.identity.toInt(),
					labels: value.labels,
				}, value.properties);

			} else if (value.constructor.name === "Integer") {
				this.obj[key] = value.toInt();
			} else {
				this.obj[key] = value;
			}
		}.bind(this));
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
		if (parameters) {
			let castParams = {};
			Object.keys(parameters).forEach(function(key) {
				castParams[key] = this.numbersToInt(parameters[key]);
			}.bind(this));
			parameters = castParams;
		}
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

	async readTransaction(query, parameters) {
		let res = [];
		let session = this.driver.session();
		let readTxResultPromise = session.readTransaction((transaction) => {
			let result = transaction.run(query, parameters);
			return result;
		});
		await readTxResultPromise.then(function(result, summary) {
			result.records.forEach((record) => res.push(new Row(record).toObject()));
			session.close();
		}.bind(this))
		.catch(function(error) {
			session.close();
			throw error;
		});
		return res;
	}

	async writeTransaction(query, parameters) {
		let res = [];
		let session = this.driver.session();
		let writeTxResultPromise = session.writeTransaction((transaction) => {
			let result = transaction.run(query, parameters);
			return result;
		});
		await writeTxResultPromise.then(function(result) {
			res = result.records;
			session.close();
		}.bind(this))
		.catch(function(error) {
			console.log(error);
			session.close();
			throw error;
		});
		return res;
	}
}

module.exports = class Neo4jConnectionDelegate extends ConnectionDelegate
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