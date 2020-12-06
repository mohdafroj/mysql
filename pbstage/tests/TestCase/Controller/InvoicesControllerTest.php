<p align="center">
  <img alt="babylon" src="https://raw.githubusercontent.com/babel/logo/master/babylon.png" width="700">
</p>

<p align="center">
  Babylon is a JavaScript parser used in <a href="https://github.com/babel/babel">Babel</a>.
</p>

<p align="center">
  <a href="https://travis-ci.org/babel/babylon"><img alt="Travis Status" src="https://img.shields.io/travis/babel/babylon/master.svg?style=flat&label=travis"></a>
  <a href="https://codecov.io/gh/babel/babylon"><img alt="Codecov Status" src="https://img.shields.io/codecov/c/github/babel/babylon/master.svg?style=flat"></a>
</p>

 - The latest ECMAScript version enabled by default (ES2017).
 - Comment attachment.
 - Support for JSX and Flow.
 - Support for experimental language proposals (accepting PRs for anything at least [stage-0](https://github.com/tc39/proposals/blob/master/stage-0-proposals.md)).

## Credits

Heavily based on [acorn](https://github.com/marijnh/acorn) and [acorn-jsx](https://github.com/RReverser/acorn-jsx),
thanks to the awesome work of [@RReverser](https://github.com/RReverser) and [@marijnh](https://github.com/marijnh).

Significant diversions are expected to occur in the future such as streaming, EBNF definitions, sweet.js integration, interspatial parsing and more.

## API

### `babylon.parse(code, [options])`

### `babylon.parseExpression(code, [options])`

`parse(