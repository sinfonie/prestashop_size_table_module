{*
*
* module: sinclothessizing
* @author PrestaShop SA <contact@prestashop.com> 
*
*}



{if isset($sizes) && $sizes && $show}
    
    <section id="sinclothessizing" class="page-product-box">
        <h3 class="page-product-heading">{$contents.section_title}</h3>
            <table class="table table-striped front_table">
                <tbody>
                    <!-- Sizing for model -->
                    <tr>
                        <td colspan="8" class="text-center">{$contents.table_title}</td>
                    </tr>
                    <!-- Sizes -->
                    <tr>
                        <td>{$product_reference}</td>
                        {foreach from=$visible.names key=size item=bool}
                            {if $size eq $bool}
                                <td class="sizes_bold">{$size|strtoupper}</td>
                            {/if}
                        {/foreach}
                    </tr>
                    <!-- Bust -->
                    {if $visible.bust eq true}
                        <tr>
                            <td class="sizes_left">{$contents.table_bust}</td>
                            {foreach from=$visible.names key=size item=bool}
                                {if $size eq $bool}
                                    <td>{$sizes.$size.bust} </td>
                                {/if}
                            {/foreach}
                        </tr>
                    {/if}
                    <!-- Waist -->
                    {if $visible.waist eq true}
                        <tr>
                            <td class="sizes_left">{$contents.table_waist}</td>
                            {foreach from=$visible.names key=size item=bool}
                                {if $size eq $bool}
                                    <td>{$sizes.$size.waist} </td>
                                {/if}
                            {/foreach}
                        </tr>
                    {/if}
                    <!-- Hips -->
                    {if $visible.hips eq true}
                        <tr>
                            <td class="sizes_left">{$contents.table_hips}</td>
                            {foreach from=$visible.names key=size item=bool}
                                {if $size eq $bool}
                                    <td>{$sizes.$size.hips} </td>
                                {/if}
                            {/foreach}
                        </tr>
                    {/if}
                    <!-- Length -->
                    {if $visible.length eq true}
                        <tr>
                            <td class="sizes_left">{$contents.table_length}</td>
                            {foreach from=$visible.names key=size item=bool}
                                {if $size eq $bool}
                                    <td>{$sizes.$size.length} </td>
                                {/if}
                            {/foreach}
                        </tr>
                    {/if}
                </tbody>
            </table>
    </section>
{/if}
<!-- sinclothessizing -->