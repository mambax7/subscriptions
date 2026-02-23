<div class="subscriptions-admin">
  <h1 class="xm-admin-title"><{$smarty.const._AM_SUBSCRIPTIONS_COUPON_LIST}></h1>
  <div class="xm-toolbar">
    <a href="coupons.php?op=edit" class="xm-btn xm-btn--sm xm-btn--primary"><{$smarty.const._AM_SUBSCRIPTIONS_COUPON_ADD}></a>
  </div>

  <{if $xm_coupons}>
  <div class="xm-table-wrap">
    <table class="xm-table xm-sortable" role="table">
      <thead>
        <tr>
          <th scope="col">ID</th>
          <th scope="col"><{$smarty.const._AM_SUBSCRIPTIONS_COUPON_CODE}></th>
          <th scope="col"><{$smarty.const._AM_SUBSCRIPTIONS_COUPON_NAME}></th>
          <th scope="col"><{$smarty.const._AM_SUBSCRIPTIONS_COUPON_TYPE}></th>
          <th scope="col"><{$smarty.const._AM_SUBSCRIPTIONS_COUPON_VALUE}></th>
          <th scope="col">Uses</th>
          <th scope="col">Active</th>
          <th scope="col" class="sorter-false"><{$smarty.const._AM_SUBSCRIPTIONS_ACTIONS}></th>
        </tr>
      </thead>
      <tbody>
        <{foreach item=c from=$xm_coupons}>
        <tr>
          <td><{$c.coupon_id}></td>
          <td><code><{$c.code|escape}></code></td>
          <td><{$c.name|escape}></td>
          <td><{$c.discount_type}></td>
          <td><{$c.discount_value}><{if $c.discount_type eq 'percentage'}>%<{/if}></td>
          <td><{$c.uses_count}>/<{if $c.max_uses eq 0}>∞<{else}><{$c.max_uses}><{/if}></td>
          <td>
            <{if $c.is_active}>
            <span class="xm-badge xm-badge--active">Active</span>
            <{else}>
            <span class="xm-badge xm-badge--inactive">Off</span>
            <{/if}>
          </td>
          <td class="xm-actions">
            <a href="coupons.php?op=edit&amp;coupon_id=<{$c.coupon_id}>" class="xm-btn xm-btn--xs xm-btn--default"><{$smarty.const._AM_SUBSCRIPTIONS_EDIT}></a>
            <a href="coupons.php?op=delete&amp;coupon_id=<{$c.coupon_id}>&amp;token=<{$c.del_token|escape:'url'}>"
               class="xm-btn xm-btn--xs xm-btn--danger"
               onclick="return confirm('<{$smarty.const._AM_SUBSCRIPTIONS_CONFIRM_DELETE|escape:javascript}>')">
               <{$smarty.const._AM_SUBSCRIPTIONS_DELETE}>
            </a>
          </td>
        </tr>
        <{/foreach}>
      </tbody>
    </table>
  </div>
  <{else}>
  <p class="xm-empty">No coupons yet. <a href="coupons.php?op=edit"><{$smarty.const._AM_SUBSCRIPTIONS_COUPON_ADD}></a></p>
  <{/if}>
</div>
