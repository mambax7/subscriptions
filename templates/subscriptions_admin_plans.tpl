<div class="subscriptions-admin">

  <h1 class="xm-admin-title"><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_LIST}></h1>
  <div class="xm-toolbar">
    <a href="plans.php?op=edit" class="xm-btn xm-btn--sm xm-btn--primary"><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_ADD}></a>
  </div>

  <{if $xm_plans}>
  <div class="xm-table-wrap">
    <table class="xm-table xm-sortable" role="table">
      <thead>
        <tr>
          <th scope="col">ID</th>
          <th scope="col"><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_NAME}></th>
          <th scope="col"><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_PRICE}></th>
          <th scope="col"><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_CYCLE}></th>
          <th scope="col"><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_ACTIVE}></th>
          <th scope="col"><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_ORDER}></th>
          <th scope="col" class="sorter-false"><{$smarty.const._AM_SUBSCRIPTIONS_ACTIONS}></th>
        </tr>
      </thead>
      <tbody>
        <{foreach item=plan from=$xm_plans}>
        <tr>
          <td><{$plan.plan_id}></td>
          <td>
            <{$plan.name|escape}>
            <{if $plan.is_featured}> <span class="xm-badge xm-badge--featured">★</span><{/if}>
          </td>
          <td><{$plan.price}> <{$plan.currency}></td>
          <td><{$plan.billing_cycle}></td>
          <td>
            <{if $plan.is_active}>
            <span class="xm-badge xm-badge--active">Active</span>
            <{else}>
            <span class="xm-badge xm-badge--inactive">Inactive</span>
            <{/if}>
          </td>
          <td><{$plan.sort_order}></td>
          <td class="xm-actions">
            <a href="plans.php?op=edit&amp;plan_id=<{$plan.plan_id}>" class="xm-btn xm-btn--xs xm-btn--default"><{$smarty.const._AM_SUBSCRIPTIONS_EDIT}></a>
            <a href="plans.php?op=delete&amp;plan_id=<{$plan.plan_id}>&amp;token=<{$plan.del_token|escape:'url'}>"
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
  <p class="xm-empty"><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_LIST}> — No plans found.</p>
  <{/if}>
</div>
