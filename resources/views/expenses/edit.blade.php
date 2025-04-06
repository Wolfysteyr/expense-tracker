<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Expense</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    <div class="container">
        <h1>Edit Expense</h1>

        <form method="POST" action="{{ route('expenses.update', $expense->id) }}">
            @csrf
            @method('PUT')

            <input type="text" name="name" value="{{ $expense->name }}" placeholder="Expense Name" required>
            <input type="number" step="0.01" name="amount" value="{{ $expense->amount }}" placeholder="Amount" required>

            <select name="category_id">
                <option value="">Select Category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ $category->id == $expense->category_id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit">Update Expense</button>
        </form>

        <a href="{{ route('expenses.index') }}">Back to Expenses List</a>
    </div>
</body>

</html>
