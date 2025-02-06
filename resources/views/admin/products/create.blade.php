<form method="POST">
    @csrf
    <input type="text" name="name" placeholder="name">
    <textarea name="description" placeholder="description"></textarea>
    <input type="number" name="stock" placeholder="stock">
    <input type="number" name="price" placeholder="price">
    <input type="text" name="image" placeholder="image url">
    <select name="live">
        <option value="true">live</option>
        <option value="false">hidden</option>
    </select>
    <button>Submit</button>
</form>
