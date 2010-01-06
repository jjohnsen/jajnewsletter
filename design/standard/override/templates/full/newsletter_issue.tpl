<html>
<head>
    <!-- meta http-equiv="Content-Type" content="text/html; charset=utf-8" -->
    <title>{$node.data_map.subject.content|wash()}</title>
    <link rel="stylesheet" media="screen" type="text/css" href={"stylesheets/newsletter.css"|ezdesign}/>
</head>
<body>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="center">
            <table id="main" width="580" border="0" cellspacing="0" cellpadding="0">
            {if $view_parameters.e|not()}
            <tr>
                <td id="webversion" align="center">
                    <p>
                        Having trouble viewing this email? 
                        <a href={concat($node.url_alias,'/(e)/1')|ezurl}>View it in your browser</a>.
                    </p>
               </td>
            </tr>
            {/if}
            <tr>
                <td>
                    <table width="580" border="0" cellspacing="16" cellpadding="0" id="content">
                    <tr class="header">
                        <td>
                        <table width="580" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                        <td class="subject">
                            <h1>{$node.data_map.subject.content|wash()}</h1>
                        </td>
                        <td align="right">
                            {def $pagedesign=fetch_alias(by_identifier,hash(attr_id,sitestyle_identifier))}
                                <a href={"/"|ezurl}><img 
                                    src={$pagedesign.data_map.image.content[original].full_path|ezroot} 
                                    alt="{$pagedesign.data_map.image.content[original].text}" /></a>
                            {/undef $pagedesign}
                        </td>
                        </tr>
                        </table>
                        </td>
                        
                    </tr>
                    {*
                    <tr class="subject">
                        <td>
                            <h1>{$node.data_map.subject.content|wash()}</h1>
                        </td>
                    </tr>
                    *}
                    {if $node.data_map.pretext.has_content}
                    <tr class="pretext">
                        <td>
                            {attribute_view_gui attribute=$node.data_map.pretext}
                        </td>
                    </tr>
                    {/if}
                    
                    {foreach $node.data_map.topics.content.relation_list as $index => $relation}
                    {def $topic=fetch( 'content', 'node', hash( 'node_id', $relation.node_id ) )}
                    <tr class="topic">
                        <td>
                            {node_view_gui view=line content_node=$topic}
                        </td>
                    </tr>
                    {undef $topic}
                    {/foreach}
 
                    {if $node.data_map.posttext.has_content}
                    <tr class="posttext">
                        <td>
                            {attribute_view_gui attribute=$node.data_map.posttext}
                        </td>
                    </tr>
                    {/if}
                    </table>
                </td>    
            </tr>
            </table>
        </td>
    </tr>
    {if $view_parameters.e|not()}
    {*
    <tr>
        <td align="center" id="unsubscribe">
            <p>
                Ønsker du ikke å motta nyhetsbrev fra oss?
                <a href={'/newsletter/manage_subscription/__remote_id/__object_id'|ezurl()}>Klikk her</a> for å melde deg av vårt nyhetsbrev.
            </p>
        </td>
    </tr>
    *}
    {/if}
    </table>
</body>
</html>
