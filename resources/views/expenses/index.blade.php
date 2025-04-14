<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Expense Tracker</title>
    <link href="css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @if (session('success'))
        <div class="alert alert-success" id="success-alert">
            <span class="alert-icon"></span> {{ session('success') }}
            <button onclick="this.parentElement.style.display='none';">Close</button>
        </div>

        <script>
            // Automatically hide the alert after 3 seconds
            setTimeout(function () {
                var alert = document.getElementById('success-alert');
                if (alert) {
                    alert.style.display = 'none';
                }
            }, 3000); // 3000ms = 3 seconds
        </script>
    @endif

</head>

<body>
    <div class="pie-chart-container" id="pie-chart-container" style="width: 30%; float: left; margin-top: 20px;">
        <canvas id="expensePieChart"></canvas>
        <button id="resetButton" hidden=true>Reset</button>
    </div>

    <div class="container">
        <h1>Expense Tracker</h1>

        <form method="POST" action="/expenses">
            @csrf
            <input type="text" name="name" placeholder="Expense Name" required>
            <input type="number" step="0.01" name="amount" placeholder="Amount" required>
            <select name="category">
                <option value="0">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit">Add Expense</button>
        </form>


        <hr>
        <form id="filterForm" method="GET" action="{{ route('expenses.index') }}">
            @csrf
            <!-- <label for="category"> Filter by Category</label> -->
            <select name="category" id="category-select" onchange="this.form.submit()" hidden=true>
                <option value='0'>All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <!-- <input type="text" name="category-select" id="category-select" value="0" hidden=true> -->

        </form>



        <ul>
            @foreach ($expenses as $expense)
                <li>
                    <span>{{ $expense->name }} - ${{ number_format($expense->amount, 2) }} |
                        {{  $categories->firstWhere('id', $expense->category_id)->name ?? 'No Category' }}</span>
                    <form method="POST" action="/expenses/{{ $expense->id }}">
                        @csrf
                        @method('DELETE')
                        <button class="delete-btn">X</button>
                        <a href="{{ route('expenses.edit', $expense->id) }}" class="edit-btn">Edit</a>

                    </form>

                </li>
            @endforeach
        </ul>

        <h2>Total: ${{ number_format($total, 2) }}</h2>
    </div>
</body>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const categoryTotals = @json($categoryTotals);
        const nameTotals = @json($nameTotals);
        const categorySelect = document.getElementById('category-select');
        const pieChartContainer = document.getElementById('pie-chart-container');

        const ctx = document.getElementById("expensePieChart").getContext("2d");
        // Function to toggle visibility of the pie chart
        const togglePieChartVisibility = () => {
            if (categorySelect.value == 0) {
                const labels = Object.keys(categoryTotals);
                const data = Object.values(categoryTotals);
                // Create the pie chart
                new Chart(ctx, {
                    type: "pie",
                    data: {
                        labels: labels.map(id => {
                            const category = @json($categories).find(cat => cat.id == id);
                            return category ? category.name : 'Unknown';
                        }), // Get category names
                        datasets: [{
                            data: data,
                            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],  // Chart colors
                            borderColor: '#fff',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (tooltipItem) {
                                        return tooltipItem.label + ': $' + tooltipItem.raw.toFixed(2);
                                    }
                                }
                            }
                        },

                        onClick: (evt, elements, chart) => {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                console.log('Clicked value:', index);


                                categorySelect.value = index;
                                document.getElementById('filterForm').submit();  
                                
                            }
                        }
                    }
                });



            } else {
                document.getElementById('resetButton').hidden = false;

                const labels = Object.keys(nameTotals);
                const data = Object.values(nameTotals);

                // Create the pie chart
                new Chart(ctx, {
                    type: "pie",
                    data: {
                        labels: labels.map(id => {
                            const expense = @json($expenses).find(exp => exp.name == id);
                            return expense ? expense.name : 'Unknown';
                        }), // Get category names
                        datasets: [{
                            data: data,
                            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],  // Chart colors
                            borderColor: '#fff',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (tooltipItem) {
                                        return tooltipItem.label + ': $' + tooltipItem.raw.toFixed(2);
                                    }
                                }
                            }
                        }
                    }
                });
                document.getElementById('resetButton').addEventListener('click', () => {
                // Reset the select dropdown
                categorySelect.value = '0';
                document.getElementById('filterForm').submit();  



                });
            }
        };



        // Call the function initially to set the correct visibility
        togglePieChartVisibility();

        // Listen for changes in the category select dropdown
        categorySelect.addEventListener('change', togglePieChartVisibility);

        // Prepare the data for the pie chart






    });

</script>


</html>