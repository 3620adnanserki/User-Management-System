@extends('layouts.admin')
@section('content')
<h6 class="c-grey-900">
    {{ trans('cruds.user.title_singular') }} {{ trans('global.list') }}
</h6>
<div class="mT-30">
    @can('user_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route("admin.subscriptions.create") }}">
                    {{ trans('global.add') }} {{ trans('cruds.user.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="table-responsive">
        <table class=" table table-bordered table-striped table-hover datatable datatable-User">
            <thead>
            <tr>
                <th width="10">

                </th>
                <th>
                    ID
                </th>
                <th>
                    Name
                </th>
                <th>
                    Email
                </th>
                <th>
                    Start Date
                </th>
                <th>
                    End Date
                </th>
                <th>
                    Status
                </th>
                <th>
                    &nbsp;
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($subscriptions as $key => $subscription)
                <tr data-entry-id="{{ $subscription->id }}">
                    <td>

                    </td>
                    <td>
                        {{ $subscription->id ?? '' }}
                    </td>
                    <td>
                        {{ $subscription->user->name ?? '' }}
                    </td>
                    <td>
                        {{ $subscription->user->email ?? '' }}
                    </td>
                    <td>
                        {{ $subscription->start_date ?? '' }}
                    </td>
                    <td>
                        {{ $subscription->end_date ?? '' }}
                    </td>
                    <td>
                        @if(\Carbon\Carbon::parse($subscription->end_date)->gte(\Carbon\Carbon::now()->format('Y-m-d')))
                            <span class="badge badge-primary">Active</span>
                        @elseif(\Carbon\Carbon::now()->format('Y-m-d')->gt(\Carbon\Carbon::parse($subscription->end_date)))
                            <span class="badge badge-danger">Expired</span>
                        @elseif(\Carbon\Carbon::now()->format('Y-m-d')->lt(\Carbon\Carbon::parse($subscription->start_date)))
                            <span class="badge badge-warning">Pending</span>
                        @else
                           <span class="badge badge-warning">-</span>
                        @endif
                    </td>
                    <td>
                        {{-- @can('user_show') --}}
                            <a class="btn btn-xs btn-primary" href="{{ route('admin.subscriptions.show', $subscription->id) }}">
                                {{ trans('global.view') }}
                            </a>
                        {{-- @endcan --}}

                        @can('user_edit')
                            <a class="btn btn-xs btn-info" href="{{ route('admin.subscriptions.edit', $subscription->id) }}">
                                {{ trans('global.edit') }}
                            </a>
                        @endcan

                        @can('user_delete')
                            <form action="{{ route('admin.subscriptions.destroy', $subscription->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                            </form>
                        @endcan

                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('user_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.users.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  $('.datatable-User:not(.ajaxTable)').DataTable({ buttons: dtButtons })
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})

</script>
@endsection
