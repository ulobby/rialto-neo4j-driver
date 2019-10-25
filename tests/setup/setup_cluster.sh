export NEO_PATH="$HOME/ulobby/neo4j_docker"
function setup_cluster()
{
HOST=$1
INSTANCE=instance$HOST
mkdir -p $INSTANCE $INSTANCE/conf $INSTANCE/logs $INSTANCE/data
cp ./neo4j.conf.template $INSTANCE/conf/neo4j.conf

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