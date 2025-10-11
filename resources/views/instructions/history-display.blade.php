<h3>Query Details</h3>

<p>This page shows the detailed results of your saved query. Here's what you can do:</p>

<ul>
    <li><strong>View query results</strong> in a tabular format with sorting capabilities</li>
    <li><strong>Export to Excel</strong> using the export button to download the results</li>
    @if($history->charttype !== 'Table')
        <li><strong>View Chart</strong> to see a visual representation of your query results</li>
    @endif
    <li><strong>Sort columns</strong> by clicking on column headers</li>
    <li><strong>Navigate back</strong> to your query history using the back button</li>
</ul>

<h4>Tips:</h4>
<ul>
    <li>Use the browser's search function (Ctrl+F or Cmd+F) to find specific data in the results</li>
    <li>For large result sets, consider exporting to Excel for better data manipulation</li>
    @if($history->charttype !== 'Table')
        <li>Switch to chart view for a visual representation of your data</li>
    @endif
    <li>Check the query execution time and result count in the page header</li>
</ul>
