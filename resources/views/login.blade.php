<html><body>

    <form method="post">
        <label>Name</label><input name="user"><br>
        <label>Password</label><input type="password" name="password"><br>
        <label>db</label><input type="hidden" name="db" value="_vps"><br>
        {{ csrf_field() }}
        <input type="submit">
    </form>
</body></html>