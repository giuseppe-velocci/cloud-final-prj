db.getSiblingDB("$db").runCommand( {
    createUser: "$user",
    pwd: "$pwd",
    roles: [
        "readWrite"
    ]
})