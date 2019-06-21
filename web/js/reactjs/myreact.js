'use strict';
// this is React example
class LikeButton extends React.Component {
    constructor(props) {
        super(props);
        this.state = { liked: false };
    }

    render() {
        if (this.state.liked) {
            return 'You liked this.';
        }

        return React.createElement(
            'button',
            { onClick: () => this.setState({ liked: true }) },
            'Like'
        );
    }
}

const divAlert = React.createElement(
    'div',
    {className: 'alert'},
);

// let div = $("<div>").addClass("alert alert-"+type).attr("role", "alert");