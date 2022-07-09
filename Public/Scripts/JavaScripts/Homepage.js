// Homepage class
class Homepage extends React.Component {
    // Render method
    render() {
        return [<Header />, <Main />];
    }
}
// Header class
class Header extends Homepage {
    // Render method
    render() {
        return (
            <header>
                Welcome to the Password Manager
            </header>
        );
    }
}
// Main class
class Main extends Homepage {
    // Render method
    render() {
        return (
            <main>
                <div>
                    <a href="/Register">Register</a>
                </div>
                <div>
                    <a href="/Login">Login</a>
                </div>
            </main>
        );
    }
}
// Rendering /
ReactDOM.render(<Homepage />, document.getElementById("app"));