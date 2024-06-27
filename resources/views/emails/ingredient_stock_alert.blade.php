<!DOCTYPE html>
<html>
<head>
    <title>Ingredient Stock Alert</title>
</head>
<body>
    <h1>Ingredient Stock Alert</h1>
    <p>The stock for {{ $ingredient->name }} has reached the re-order point.</p>
    <p>Current Stock: {{ $ingredient->stock }}</p>
    <p>Re-Order Point: {{ $ingredient->reOrder_point }}</p>
</body>
</html>
