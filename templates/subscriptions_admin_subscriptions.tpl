<div class="subscriptions-admin">

  <div class="xm-admin-header">
    <h1 class="xm-admin-title"><{$smarty.const._AM_SUBSCRIPTIONS_SUB_LIST}></h1>
    <span class="xm-count"><{$smarty.const._AM_SUBSCRIPTIONS_ACTIVE_SUBS}>: <{$xm_total}></span>
  </div>

  <{if $xm_user_filter > 0}>
  <div class="xm-notice xm-notice--info">
    <{$smarty.const._AM_SUBSCRIPTIONS_FILTER_USER}>: <strong>#<{$xm_user_filter}></strong>
    &nbsp;<a href="subscriptions.php" class="xm-btn xm-btn--xs xm-btn--default"><{$smarty.const._AM_SUBSCRIPTIONS_FILTER_CLEAR}></a>
  </div>
  <{/if}>

  <!-- Status filter -->
  <nav class="xm-filter-nav" aria-label="Filter subscriptions by status">
    <{foreach item=s from=['','active','trial','past_due','cancelled','expired','paused']}>
    <a href="subscriptions.php?status=<{$s}>" class="xm-filter-btn<{if $xm_status_filter eq $s}> xm-filter-btn--active<{/if}>">
      <{if $s eq ''}><{$smarty.const._AM_SUBSCRIPTIONS_DASHBOARD}><{else}><{$s|capitalize}><{/if}>
    </a>
    <{/foreach}>
  </nav>

  <{if $xm_subs}>
  <div class="xm-table-wrap">
    <table class="xm-table xm-sortable" role="table">
      <thead>
        <tr>
          <th scope="col">ID</th>
          <th scope="col">User</th>
          <th scope="col">Plan</th>
          <th scope="col">Status</th>
          <th scope="col">Expires</th>
          <th scope="col">Amount</th>
          <th scope="col">Joined</th>
          <th scope="col" class="sorter-false"><span class="sr-only"><{$smarty.const._AM_SUBSCRIPTIONS_ACTIONS}></span></th>
        </tr>
      </thead>
      <tbody>
        <{foreach item=sub from=$xm_subs}>
        <tr>
          <td><{$sub.sub_id}></td>
          <td><{$sub.username|escape}></td>
          <td><{$sub.plan_name|escape}></td>
          <td><span class="xm-badge xm-badge--<{$sub.status}>"><{$sub.status|capitalize}></span></td>
          <td><{$sub.period_end}></td>
          <td><{$sub.amount_paid}></td>
          <td><{$sub.created_at}></td>
          <td class="xm-actions">
            <{if $sub.status neq 'cancelled' and $sub.status neq 'expired'}>
            <form method="post" action="subscriptions.php?op=cancel&amp;sub_id=<{$sub.sub_id}>" style="display:inline">
              <input type="hidden" name="token"  value="<{$sub.cancel_token}>">
              <input type="hidden" name="reason" value="Admin cancellation">
              <button type="submit" class="xm-btn xm-btn--xs xm-btn--danger"
                      onclick="return confirm('<{$smarty.const._AM_SUBSCRIPTIONS_CONFIRM_DELETE|escape:javascript}>')">
                <{$smarty.const._AM_SUBSCRIPTIONS_SUB_CANCEL}>
              </button>
            </form>
            <{/if}>
          </td>
        </tr>
        <{/foreach}>
      </tbody>
    </table>
  </div>
  <div class="xm-pagenav"><{$xm_pagenav}></div>
  <{else}>
  <p class="xm-empty">No subscriptions found.</p>
  <{/if}>
</div>
