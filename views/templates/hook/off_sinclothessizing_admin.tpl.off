{*
*
* module: sinclothessizing
* @author PrestaShop SA <contact@prestashop.com>
    *
    *}

    <div class="panel product-tab">
        <h3>{$contents.display_name}</h3>

        <label class="control-label col-lg-1"></label>
        <div class="form-group">
            <div class="row">
                <div class="form-group col-lg-2 text-center">
                    <span>{$contents.table_bust}</span>
                </div>
                <div class="form-group col-lg-2 text-center">
                    <span>{$contents.table_waist}</span>
                </div>
                <div class="form-group col-lg-2 text-center">
                    <span>{$contents.table_hips}</span>
                </div>
                <div class="form-group col-lg-2 text-center">
                    <span>{$contents.table_length}</span>
                </div>
            </div>
        </div>

        <label class="control-label col-lg-1"></label>
        <div class="form-group">
            <div class="row">
                <div class="form-group col-lg-2">
                    <div class="input-group">
                        <span class="input-group-addon">S</span>
                        <input id="bust_s" name="bust_s" type="text" value="{$sizes.s.bust}" maxlength="3" />
                    </div>
                </div>
                <div class="form-group col-lg-2">
                    <input id="waist_s" name="waist_s" type="text" value="{$sizes.s.waist}" maxlength="3" />
                </div>
                <div class="form-group col-lg-2">
                    <input id="hips_s" name="hips_s" type="text" value="{$sizes.s.hips}" maxlength="3" />
                </div>
                <div class="form-group col-lg-2">
                    <input id="length_s" name="length_s" type="text" value="{$sizes.s.length}" maxlength="3" />
                </div>
            </div>
        </div>

        <label class="control-label col-lg-1"></label>
        <div class="form-group">
            <div class="row">
                <div class="form-group col-lg-2">
                    <div class="input-group">
                        <span class="input-group-addon">XL</span>
                        <input id="bust_xl" name="bust_xl" type="text" value="{$sizes.xl.bust}" maxlength="3" />
                    </div>
                </div>
                <div class="form-group col-lg-2">
                    <input id="waist_xl" name="waist_xl" type="text" value="{$sizes.xl.waist}" maxlength="3" />
                </div>
                <div class="form-group col-lg-2">
                    <input id="hips_xl" name="hips_xl" type="text" value="{$sizes.xl.hips}" maxlength="3" />
                </div>
                <div class="form-group col-lg-2">
                    <input id="length_xl" name="length_xl" type="text" value="{$sizes.xl.length}" maxlength="3" />
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">{$contents.display_name}</label>
            <div class="form-group">
                <div class=" col-lg-4	table-responsive-row clearfix">
                    <table class="table text-center table-striped table-bordered table-sm configuration">
                        <tbody>
                            <!-- Sizing for model -->
                            <!-- Sizes -->
                            <tr>
                                <td class="sizes_bold"></td>
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
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel' mod='jashtexttabs'}</a>
            <button type="submit" name="submitAddproduct" class="btn btn-default pull-right"><i class="process-icon-loading"></i> {l s='Save' mod='jashtexttabs'}</button>
            <button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right"><i class="process-icon-loading"></i> {l s='Save and stay' mod='jashtexttabs'}</button>
        </div>
    </div>