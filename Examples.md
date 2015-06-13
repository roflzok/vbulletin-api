# getForum #

JSON-RPC:

```
{
    "jsonrpc": "2.0",
    "method": "getForum",
    "params": [
        {
            "forumId": 1
        }
    ],
    "id": 123
}
```

XML-RPC:

```
<?xml version="1.0" ?>
<methodCall>
    <methodName>getForum</methodName>
    <params>
        <param>
            <value>
                <struct>
                    <member>
                        <name>forumId</name>
                        <value><int>1</int></value>
                    </member>
                </struct>
            </value>
        </param>
    </params>
</methodCall>
```