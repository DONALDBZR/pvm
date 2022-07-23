class Application extends React.Component {
    render() {
        return [<Header />, <Main />];
    }
}
class Header extends Application {
    render() {
        return (
            <header id="homepageHeader">Welcome to the Password Manager</header>
        );
    }
}
class Main extends Application {
    render() {
        return (
            <main id="homepageMain">
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
ReactDOM.render(<Application />, document.getElementById("app"));
