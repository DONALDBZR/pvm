// Application class
class Application extends React.Component {
    // Render method
    render() {
        return [<Header />, <Main />, <Footer />];
    }
}
// Header class
class Header extends Application {
    // Render method
    render() {
        return (
            <header>
                <div id="home">
                    <a href="../">
                        <img src="../Public/Images/(1569).png" />
                    </a>
                </div>
                <div id="pageDescription">Password Manager Registration Page</div>
            </header>
        );
    }
}
// Main class
class Main extends Application {
    // Constructor method
    constructor(props) {
        super(props);
        this.state = {
            mailAddress: "",
            success: "",
            message: "",
            url: ""
        };
    }
    // Change handler method
    handleChange(event) {
        // Local variables
        const target = event.target;
        const value = target.value;
        const name = target.name;
        // Changing the state of the targeted name to its value
        this.setState({
            [name]: value
        });
    }
    // Submit handler method
    handleSubmit(event) {
        // Local variables
        const delay = 200;
        // Preventing default submission
        event.preventDefault();
        // Using fetch API to send data as a json as a request to obtain a json back as a response
        fetch("./Register.php", {
            method: "POST",
            body: JSON.stringify({
                mailAddress: this.state.mailAddress
            }),
            headers: {
                "Content-Type": "application/json"
            }
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    success: data.success,
                    message: data.message,
                    url: data.url,
                })
            )
            .then(() => this.redirector(delay));
    }
    // Redirector method
    redirector(delay) {
        // Setting the timeout before redirecting the user
        setTimeout(() => {
            window.location.url = this.state.url
        }, delay);
    }
    // Render method
    render() {
        return (
            <main>
                <form method="POST" onSubmit={this.handleSubmit.bind(this)}>
                    <div id="mailAddress">
                        <div>
                            Mail Address:
                        </div>
                        <input type="email" name="mailAddress" placeholder="Mail Address" value={this.state.mailAddress} onChange={this.handleChange.bind(this)} required />
                    </div>
                    <div id="button">
                        <button>Register</button>
                    </div>
                    <div id="serverRendering">
                        <h1 id={this.state.success}>{this.state.message}</h1>
                    </div>
                </form>
            </main>
        );
    }
}
// Footer class
class Footer extends Application {
    // Render method
    render() {
        return (
            <footer>
                Already have an account for that user? Prompt him/her to <a href="../Login">log in</a>
            </footer>
        );
    }
}
// Rendering /
ReactDOM.render(<Application />, document.getElementById("app"));