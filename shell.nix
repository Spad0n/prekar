{ pkgs ? import <nixpkgs> {} }:
pkgs.mkShell {
  buildInputs = with pkgs; [
    symfony-cli
    php
    mysql80
    php82Packages.composer
  ];
}
