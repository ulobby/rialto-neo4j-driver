## Setting up a local Neo4j cluster using Docker

The goal of this blog post is to set up a local Neo4j cluster on your machine so that you can test out stuff like using the `bolt+routing` protocol. It is based on [this example](https://markhneedham.com/blog/2016/11/13/neo4j-3-1-beta3-docker-creating-a-causal-cluster/), but with a bit more focus on imitating what a real setup would look like.

The success criteria are that
* We will have a script that deals with most of the setup of a local cluster
* The local cluster acts in a reasonable way, i.e. passes on the leader role when a server goes down
* Minimal changes to conf files and so on are needed from the user side

### Setting up the cluster using setup_cluster.sh

To just set up the cluster so that you can get to working on other stuff, start by sourcing the script

```bash
source setup_cluster.sh 
```

next up you will need to edit your hosts file to make the hostnames of the DB servers point to right addresses, so `sudo nano /etc/hosts` and add

```
127.0.0.2       instance0
127.0.0.3       instance1
127.0.0.4       instance2
```
After this you will have to open up a new shell for the changes to take effect. Now set up the network that the cluster will run on
```bash
docker network create --driver=bridge cluster
```
and set the path that you want to cluster to live at
```bash
export NEO_PATH="$HOME/ulobby/neo4j_docker"
```
you'll need to add at least three instances to the cluster for it to run, so run
```
setup_cluster 0
setup_cluster 1
setup_cluster 2
```
To check if the clusters are running you can do `docker logs instance0` and see if an interface is available. Once it is you can run `CALL dbms.cluster.routing.getServers()` and you should get a number of READ, WRITE and ROUTING servers back. If you are missing READ servers then it is probably because something went wrong with your hosts file or because your `neo4j.conf` files do not have `default_advertised_address` set to the instance name.
If everything is up and running you will have to import data, which I haven't set up a script for yet, so you can find more info [here](https://neo4j.com/docs/operations-manual/current/clustering/seed-cluster/).

### Running stand alone Neo4j 3.5 in Docker

Alright, time for a slightly more in depth explanation of how we mangled together this script. Feel free to skip if you're not interested. To make things easier for ourselves we will use the official Neo4J docker image to automatize setup. A simple example of how to set up a normal, non-clustered database is seen below.

```bash
mkdir conf data logs
docker run \
    --detach \
    --publish=7474:7474 --publish=7687:7687 \
    --volume=$HOME/ulobby/neo4j_docker/data:/data \
    --volume=$HOME/ulobby/neo4j_docker/logs:/logs \
    --volume=$HOME/ulobby/neo4j_docker/conf:/conf \
    --env=NEO4J_ACCEPT_LICENSE_AGREEMENT=yes \
    --user $(id -u):$(id -g) \
    neo4j:3.5-enterprise
```

This will result in a folder structure owned by the calling user that looks like
```
/ -
  - /data
  - /logs
  - /conf
```
where we can find the database data, the logs and the configuration files.
We also make sure that the server actually runs as the calling user by including `--env=NEO4J_ACCEPT_LICENSE_AGREEMENT=yes \`. Note that if you don't want to use a clustered server you can create Neo4Js standard configuration file by calling
```bash
docker run --rm \
  --user $(id -u):$(id -g) \
  --volume=$HOME/ulobby/neo4j_docker/conf:/conf \
  neo4j:3.5-enterprise dump-config
```
and then running this as your standard Neo4j instance. The only change you will need to make is to make sure that Neo4j is listening to incoming connections by uncommenting `dbms.connectors.default_listen_address=0.0.0.0` in `conf/neo4j.conf` 

## Runnning a Neo4j local cluster in Docker

To switch to a cluster we mainly need to make a couple of changes to the config file. We will create new config files in a folder for this instance. 

```bash
mkdir -p instance0 /conf instance0/logs instance0/data 
```

Signaling that this server is going to be part of a cluster core by setting `dbms.mode=CORE`. We are uninventive, so we will call each machine in the cluster `instance`, so we will tell the DB to look for machines with that name 
`causal_clustering.initial_discovery_members=instance0:5000,instance1:5000,instance2:5000` 

Finally we will need to change the advertised address to `dbms.connectors.default_advertised_address` and then the configuration is good to go.

To set up the first instance we will create a new docker network for the cluster by running
```bash
docker network create --driver=bridge cluster
```
now we can finally start up the instance, which is very similar to starting the stand alone database except we specify that the docker instance runs on our newly created network
```bash
docker run --name=instance0 --detach \
           --network=cluster \
           --publish=127.0.0.2:7474:7474 \
           --publish=127.0.0.2:7687:7687 \
           --publish=127.0.0.2:7473:7473 \
           --env=NEO4J_ACCEPT_LICENSE_AGREEMENT=yes \
           --user (id -u):(id -g) \
           --volume=$HOME/ulobby/neo4j_docker/instance0/conf:/conf \
           --volume=$HOME/ulobby/neo4j_docker/instance0/data:/data \
           --volume=$HOME/ulobby/neo4j_docker/instance0/logs:/logs \
           neo4j:3.5-enterprise
```

there should now be a database available at 127.0.0.2:7474, but since there is only one server available it will only be read.
Setting up the remaining databases we just have to switch out `instance0` for `instanceX` and incrementing the IP address.

for each instance, changing the hostname and name each time. At this point however I would recommend just using the script as described in first paragraph.

### Script for setting up servers

```bash
export NEO_PATH="$HOME/ulobby/neo4j_docker"
function setup_cluster() 
{
HOST=$1
INSTANCE=instance$HOST
mkdir -p $INSTANCE $INSTANCE/conf $INSTANCE/logs $INSTANCE/data
cp neo4j.conf.template $INSTANCE/conf/neo4j.conf

REPLACE_ME="# dbms.connectors.default_advertised_address="
DEFAULT_ADDRESS="dbms.connectors.default_advertised_address=$INSTANCE"
sed -i "s/$REPLACE_ME/$DEFAULT_ADDRESS/g" $INSTANCE/conf/neo4j.conf > /dev/null

docker run --name=$INSTANCE --detach \
           --network=cluster \
           --publish=127.0.0.$[$HOST+2]:7474:7474 \
           --publish=127.0.0.$[$HOST+2]:7687:7687 \
           --publish=127.0.0.$[$HOST+2]:7473:7473 \
           --env=NEO4J_ACCEPT_LICENSE_AGREEMENT=yes \
           --env=NEO4J_dbms__connectors__default_advertised_address \
           --user $(id -u):$(id -g) \
           --volume=$NEO_PATH/$INSTANCE/conf:/conf \
           --volume=$NEO_PATH/$INSTANCE/data:/data \
           --volume=$NEO_PATH/$INSTANCE/logs:/logs \
           neo4j:3.5-enterprise
}
```