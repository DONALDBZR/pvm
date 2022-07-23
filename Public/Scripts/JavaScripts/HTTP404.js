class Application extends React.Component {
    render() {
        return [<Header />, <Main />];
    }
}
class Header extends Application {
    render() {
        return (
            <header id="http404Header">
                <h1>HTTP 404</h1>
            </header>
        );
    }
}
class Main extends Application {
    render() {
        return (
            <main id="http404Main">
                <h1>Not Found!</h1>
            </main>
        );
    }
}
// Rendering the application
ReactDOM.render(<Application />, document.getElementById("app"));
