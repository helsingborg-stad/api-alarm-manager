# bin/bash
rm -rf .devcontainer/remoteFiles/files/* && \
rm -rf .devcontainer/remoteFiles/archive/* && \
find .devcontainer/remoteFiles/dummyFiles -type f -name "*.XML" -exec cp {} .devcontainer/remoteFiles/files \;