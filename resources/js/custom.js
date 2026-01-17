document.addEventListener('DOMContentLoaded', () => {
    // const gridOptions = {
    //     columnDefs: [
    //         { field: 'id', filter: true, sortable: true },
    //         { field: 'name', filter: true, sortable: true },
    //     ],
    //     rowData: [], // Dữ liệu sẽ lấy qua API
    //     rowModelType: 'infinite',
    //     cacheBlockSize: 100,
    //     maxBlocksInCache: 10,
    // };

    // const gridDiv = document.querySelector('#business-grid');
    // if (gridDiv) {
    //     new Grid(gridDiv, gridOptions);

    //     gridOptions.api.setDatasource({
    //         getRows: async (params) => {
    //             const response = await fetch('/businesses-grid', {
    //                 method: 'POST',
    //                 headers: {
    //                     'Content-Type': 'application/json',
    //                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    //                 },
    //                 body: JSON.stringify({
    //                     startRow: params.request.startRow,
    //                     endRow: params.request.endRow,
    //                     filterModel: params.request.filterModel,
    //                     sortModel: params.request.sortModel,
    //                 }),
    //             });
    //             const result = await response.json();
    //             params.successCallback(result.rows, result.lastRow);
    //         },
    //     });
    // }

    // const select = document.querySelector('#business-select');
    // if (select) {
    //     const choices = new Choices(select, {
    //         searchEnabled: true,
    //         searchChoices: true,
    //         placeholderValue: 'Chọn doanh nghiệp',
    //         searchPlaceholderValue: 'Tìm kiếm doanh nghiệp',
    //         maxItemCount: 1,
    //         searchResultLimit: 100,
    //         loadChoices: async (value) => {
    //             const response = await fetch(`/businesses?search=${value}`);
    //             const businesses = await response.json();
    //             return businesses.map(b => ({ value: b.id, label: b.name }));
    //         },
    //     });
    // }
});