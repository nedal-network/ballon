@php
    $this->getTable()->modifyQueryUsing(
        fn ($query) => $query->when(
            isset($this->tableFilters['region_id']['values']) && ! empty($this->tableFilters['region_id']['values']),
            fn ($query) => $query->whereHas('likedRegions', fn ($query) => $query->whereIn('id', $this->tableFilters['region_id']['values']))
        )
    );
@endphp
<div class="ms-auto max-w-xs w-full">
    <livewire:region-filter :tableFilters="$this->tableFilters" />
</div>
